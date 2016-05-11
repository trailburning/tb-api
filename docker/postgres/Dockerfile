FROM postgres:9.5

MAINTAINER Patrick Trost <mail@backendweb.de>

RUN apt-get update && apt-get install -y postgis

ADD init-db.sh /docker-entrypoint-initdb.d/

CMD ["postgres"]
