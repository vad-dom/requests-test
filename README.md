<h1>Requests API</h1>
<p>REST API для управления заявками</p>
<p>Проект выполнен на <b>Yii2</b> (basic template) с использованием <b>Swagger</b> для документации</p>

<h2>Дополнения и упрощения:</h2>
  <li>Добавлены эндпойнты API: GET <b>/requests/{id}</b> - Получить заявку по ID, и DELETE <b>/requests/{id}</b> - Удалить заявку</li>
  <li>Не сделан web интерфейс для отправки заявок и ответа на них</li>
  <li>Пользователи не хранятся в БД, используется шаблонная модель User, которая создалась при установке yii</li>
  <li>Email сохраняется в виде файлов</li>
  <li>Сделано 3 API-теста</li>

<h2>Требования к ПО:</h2>
<ul>
  <li>PHP = 7.4</li>
  <li>MySQL = 5.7</li>
  <li>Composer</li>
  <li>Git</li>
</ul>

<h2>Краткая инструкция по установке проекта:</h2>
<ul>
  <li>
    Клонировать репозиторий requests-test: 
    <ul>
      <li><b>git clone https://github.com/vad-dom/requests-test.git</b></li>
    </ul>
  </li>
  <li>
    Обновить до последних версий и установить зависимости в Composer: 
    <ul>
      <li><b>cd backend</b></li>
      <li><b>composer update</b></li>
      <li><b>composer install</b></li>
    </ul>
  </li>
    <li>
    При настройке веб сервера корневую папку домена указать: 
    <ul>
      <li><b>/backend/web</b></li>
    </ul>
  </li>
  <li>Создать новую базу данных</li>
  <li>
    Настроить подключение к базе данных:
    <ul>
      <li><b>/backend/config/db.php</b></li>
    </ul>
  </li>
  <li>
    Применить миграцию для создания структуры базы данных:
    <ul>
      <li><b>php yii migrate</b></li>
    </ul>
  </li>
    <li>
    Swagger-документация (интерактивная, с возможностью отправлять запросы прямо из интерфейса):
    <ul>
      <li><b>/swagger</b></li>
    </ul>
  </li>
    <li>
    Запуск API-тестов:
    <ul>
      <li><b>vendor/bin/codecept run api</b></li>
    </ul>
  </li>
</ul>
