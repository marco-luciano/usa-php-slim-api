FROM alpine:3.18
RUN apk update && apk --no-cache add postgresql12-client && apk add bash && apk add openssl
ENTRYPOINT [ "/bin/bash" ]