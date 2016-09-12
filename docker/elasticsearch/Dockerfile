FROM elasticsearch

MAINTAINER Patrick Trost <mail@backendweb.de>

RUN plugin install analysis-phonetic

EXPOSE 9200 9300
ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["elasticsearch"]