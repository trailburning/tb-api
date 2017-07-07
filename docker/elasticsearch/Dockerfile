FROM elasticsearch:1.7-alpine

MAINTAINER Patrick Trost <mail@backendweb.de>

RUN plugin install elasticsearch/elasticsearch-analysis-phonetic/2.7.0

EXPOSE 9200 9300
ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["elasticsearch"]