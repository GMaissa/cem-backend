FROM mariadb:5.5

MAINTAINER Guillaume Maïssa <pro.g@maissa.fr>

# Install dependencies
RUN DEBIAN_FRONTEND=noninteractive apt-get update && \
    apt-get install -y python-dev python-pip && \
    pip install click prompt_toolkit pygments mycli && \
    apt-get clean && \
    rm -rf /tmp/* /var/tmp/*

