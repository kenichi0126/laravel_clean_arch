FROM redis:3.2

RUN apt-get update && apt-get install -y \
    locales \
    vim

RUN sed -i -e "s/# \(en_US.UTF-8 UTF-8\)/\1/g" /etc/locale.gen && locale-gen en_US.UTF-8

ENV SHELL bash
ENV LANG en_US.UTF-8
ENV TZ Asia/Tokyo
