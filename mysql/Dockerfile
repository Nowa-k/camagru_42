FROM mysql:8.0.28

ARG MYSQL_DATABASE

COPY init.sql /docker-entrypoint-initdb.d/

RUN sed -i "s|CREATE DATABASE IF NOT EXISTS MYSQL_DATABASE;|CREATE DATABASE IF NOT EXISTS ${MYSQL_DATABASE};|g" /docker-entrypoint-initdb.d/init.sql
RUN sed -i "s|USE MYSQL_DATABASE;|USE ${MYSQL_DATABASE};|g" /docker-entrypoint-initdb.d/init.sql