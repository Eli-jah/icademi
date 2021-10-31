FROM mysql:5.7.36

# docker container run --name icademi_mysql \
# -p 3336:3306 -e TZ=Asia/Shanghai MYSQL_ROOT_PASSWORD=Qwerty1234 MYSQL_DATABASE=icademi MYSQL_USER=icademi MYSQL_PASSWORD=Qwerty1234 -d \
# mysql:5.7.36 --default-authentication-plugin=mysql_native_password --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci

# docker container run -it --rm icademi_mysql mysql:5.7.36 -hdb -uicademi -p

# RUN apt update && apt list --upgradable && apt upgrade -y && apt autoremove -y
# RUN apt install -y git wget vim net-tools build-essential

# COPY source dest
COPY ./etc/profile/. /root/

ENV TZ=Asia/Shanghai
ENV MYSQL_ROOT_PASSWORD=Qwerty123456
ENV MYSQL_DATABASE=icademi
ENV MYSQL_USER=icademi
ENV MYSQL_PASSWORD=Qwerty123456

EXPOSE 3306

# CMD [ "mysql --default-authentication-plugin=mysql_native_password" ]
