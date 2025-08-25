<h1>Requests API</h1>
<p>REST API для управления заявками</p>
<p>Проект выполнен на <b>Yii2</b> (basic template) с использованием <b>Swagger</b> для документации</p>

<h2>Дополнения и упрощения:</h2>
  <li>Добавлены методы Delete и View</li>
  <li>Не сделан web интерфейс для отправки заявок и ответа на них</li>
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
      <b>php yii migrate</b>
    </ul>
  </li>
    <li>
    Swagger-документация:
    <ul>
      <b>/swagger</b>
    </ul>
  </li>
    <li>
    Запуск API-тестов:
    <ul>
      <b>vendor/bin/codecept run api</b>
    </ul>
  </li>
</ul>
