# TP3 Part 2 — Docker Challenges

A simple, separated layout: one `xss/` folder, one `csrf/` folder, and the
Docker Compose file at the project root. Apache, PHP, and MariaDB run inside
containers; nothing needs to be installed/configured on the host except Docker.

## Project structure

```text
tp3-labs/
├── compose.yaml
├── README.md
├── xss/
│   ├── Dockerfile
│   ├── index.php
│   └── init.sql
└── csrf/
    ├── Dockerfile
    ├── index.php
    ├── init.sql
    └── attacker/
        ├── index.html
        ├── lure_get.html
        ├── lure_post.html
        └── lure_token.html
```

## Start all challenges

Run these commands from the project root (the directory containing `compose.yaml`):

```bash
docker compose up -d --build
docker compose ps
```

Wait until the database containers are healthy, then open the challenge links:

| Challenge | Direct link |
|---|---|
| XSS — MedCare Helpdesk | http://localhost:8081/ |
| CSRF — Atlas NetBank | http://localhost:8082/ |
| CSRF — attacker PoC index | http://localhost:8083/ |

The XSS challenge includes reflected, stored, and DOM-based XSS sinks.
The CSRF challenge includes GET state change, POST wire transfer, and weak-token
demonstrations. The PoC pages are served separately on port 8083.

## Stop and reset

Stop containers but preserve the database contents:

```bash
docker compose down
```

Reset both databases to their original seeded state (deletes all challenge data):

```bash
docker compose down -v
docker compose up -d --build
```

## Logs and troubleshooting

```bash
docker compose logs -f
docker compose ps
docker compose restart xss
docker compose restart csrf
```

If a port is already in use, change the host-side port (left side) in `compose.yaml`.
For example, `"8081:80"` maps host port 8081 to container port 80.

## Safety

These applications are intentionally vulnerable and are for the isolated course lab.
Keep them bound to localhost; do not expose the ports to a public network or deploy
them on a public server. Use a dedicated browser profile if possible.

The CSRF proof-of-concept pages use `localhost` on port 8083 and target the NetBank
application on port 8082. Different ports are different origins but share the same
site hostname; this is useful for the lab demonstration but is not a full simulation
of a third-party cross-site deployment. Modern browsers may also apply cookie policy
restrictions depending on browser settings.
