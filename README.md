# PPSI2_Wedding_Planner
This project was created for the purpose of passing the PPSI 2 course (Design and Programming of Internet Systems 2).

### Autorzy

* Jakub Engielski *(Leader/PM/Frontend)*
* Michał Myśków *(DevOps/Backend)*
* Anna Sokołowska *(Frontend/Backend)*
* Jan Kwiatkowski *(Frontend/Backend)*
* Szymon Michno *(Tester)*

### Technologie 

* PHP 8.0
* Symfony 5.3.9
* MariaDB (MySQL)
* Twig
* Docker

### Uruchomienie aplikacji w środowisku deweloperskim

**1. Pobranie projektu z repozytorium**
```
git clone https://github.com/MichalMyskow/PPSI2_Wedding_Planner.git
```

```
cd PPSI2_Wedding_Planner
```

**2. Konfiguracja pliku .env**
```
cp .env .env.local
```

**3. Uruchomienie kontenerów dockerowych**
```
docker-compose up -d --build
```

**4. Pobranie zależności Composer**
```
docker-compose exec php composer install
```

**5. Pobranie zależności npm**
```
docker-compose exec php npm install
```

**6. Budowanie Assetów**
```
docker-compose exec php npm run dev
```

### Migracje
**Utworzenie migracji**
```
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### Bez migracji
**Utworzenie schematu bazy danych**
```
php bin/console doctrine:schema:update --force
```

### Załadowanie zdefiniowanych wczesniej danych do bazy danych
```
php bin/console doctrine:fixtures:load
```

### Udostępnione porty

***Aplikacja powinna być dostępna pod:***
```
http://localhost:8080
```

***phpMyAdmin powinien być dostępny pod:***
```
http://localhost:8081
```

### Dane logowania do phpMyAdmin
```
Serwer: database
Użytkownik: user
Hasło: 123qwe

Nazwa bazy danych: weddingplannerdb
```

### Przydatne komendy
**Docker - uruchomienie kontenerów**
```
docker-compose up -d
```

**Docker - zatrzymanie kontenerów**
```
docker-compose stop
```

**Docker - zatrzymanie i usunięcie kontenerów**
```
docker-compose down
```
