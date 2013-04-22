Проект "CARGO"
==============

Цель проекта состоит в создании сервиса для...

Установка
---------

Простейшим вариантом для создания окружения является использование виртуальной машины. Чтобы ей воспользоваться
понадобятся:

1. [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
2. [Vagrant](http://downloads.vagrantup.com/)

После установки VirtualBox и Vagrant:

1. Клонируем репозиторий

    ```
    git clone git@github.com:MashaiMedvedi/cargo.git
    ```
2. Переходим в каталог с клонированным проектом

    ```
    cd cargo
    ```
3. Устанавливаем пакеты

    ```
    php composer.phar self-update
    php composer.phar --dev install
    cd dev
    git submodule update --init --recursive
    cd ..
    ```
4. Запускаем виртуальную машину

    ```
    cp dev/Vagrantfile.dist Vagrantfile
    vagrant up
    ```
