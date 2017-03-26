ElasticSearchTest
=================

A little Symfony project I wrote to familiarize myself with Elasticsearch.
It uses the [ongr Elasticsearch bundle](https://github.com/ongr-io/ElasticsearchBundle)

- Create random documents and index them: `bin/console estest:create <number of docs>`
- Match documents: start server with `bin console server:run` and go to [localhost:8000/match](http://localhost:8000/match)
