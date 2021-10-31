FROM redis:latest

# docker container run --name icademi_redis -d redis:latest
# docker container run --name icademi_redis -d redis:latest redis-server --save 60 1 --loglevel warning
# docker container run -it --network some-network --rm redis:latest redis-cli -h icademi_redis
# docker container run -it --rm redis:latest redis-cli -h icademi_redis
# docker container run -v /myredis/conf:/usr/local/etc/redis --name icademi_redis redis redis-server /usr/local/etc/redis/redis.conf

# RUN apt update && apt list --upgradable && apt upgrade -y && apt autoremove -y
# RUN apt install -y git wget vim net-tools build-essential

# COPY source dest
COPY ./etc/profile/. /root/

# COPY redis.conf /usr/local/etc/redis/redis.conf

EXPOSE 6379

# CMD [ "redis-server", "/usr/local/etc/redis/redis.conf" ]
CMD [ "redis-server" ]
