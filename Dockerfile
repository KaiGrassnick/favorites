FROM ubuntu:latest

RUN apt-get update && apt-get install -y zip

COPY build.sh /build.sh

RUN chmod +x /build.sh

CMD /build.sh
