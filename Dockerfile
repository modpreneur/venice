FROM modpreneur/trinity-test:0.2.1

MAINTAINER Martin Kolek <kole@modpreneur.com>

WORKDIR /var/app

ENTRYPOINT ["fish", "entrypoint.sh"]