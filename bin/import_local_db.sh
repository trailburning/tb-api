psql -h docker.localhost -Utrailburning -d trailburning -c "drop schema public cascade"
psql -h docker.localhost -Utrailburning -d trailburning -c "create schema public"
psql -q -t -h docker.localhost -Utrailburning -d trailburning -f var/cache/dump.sql