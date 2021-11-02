# FROM node:6
FROM node:lts

# docker build -t my-nodejs-app .
# docker run -it --rm --name my-running-app my-nodejs-app

# To install basic tools
RUN apt update && apt list --upgradable && apt upgrade -y && apt autoremove -y
RUN apt install -y git wget vim net-tools build-essential

# To install apidoc
# https://apidocjs.com/#install
# RUN npm config set registry https://registry.npm.taobao.org --global
# RUN npm config set disturl https://npm.taobao.org/dist --global
# RUN npm config set sass_binary_site=https://npm.taobao.org/mirrors/node-sass
# RUN npm cache clean -f
RUN npm install npm -g
# RUN npm install cnpm -g
RUN npm install apidoc -g

# RUN rm -rf ~/.node-gyp/
# RUN npm install node-gyp -g
# RUN npm install bcrypt -g
# RUN npm install node-sass -g

COPY ./etc/profile/. /root/
# COPY ./etc/profile/. /home/node/

# COPY . /home/node/app
# WORKDIR /home/node/app
WORKDIR /var/www/html
COPY . .

# To install Laravel Mix
# RUN rm -rf node_modules

# -S => --save | -D => --save-dev
# RUN npm install
# RUN cnpm install
# RUN cnpm install sass-loader --save-dev
# RUN cnpm install node-sass --save-dev
# RUN yarn add sass-loader --save-dev
# RUN yarn add node-sass --save-dev

# RUN yarn config set registry https://registry.npm.taobao.org --global
# RUN yarn config set disturl https://npm.taobao.org/dist --global
# RUN SASS_BINARY_SITE=https://npm.taobao.org/mirrors/node-sass yarn --no-bin-links
# RUN SASS_BINARY_SITE=http://npm.taobao.org/mirrors/node-sass yarn install --no-bin-links
# RUN yarn install --no-bin-links

# To generate apidoc
RUN /usr/local/bin/apidoc -i app/Http/Controllers/Api/ -o public/apidoc/

ENV TZ=Asia/Shanghai
# ENV NODE_ENV=production
ENV NODE_ENV=devlopment

EXPOSE 8888

# CMD [ "npm run watch-poll" ]
# CMD [ "/usr/local/bin/apidoc -i app/Http/Controllers/Api/ -o public/apidoc/" ]
# CMD [ "npm run watch -- --watch-poll" ]
# CMD [ "npm start" ]