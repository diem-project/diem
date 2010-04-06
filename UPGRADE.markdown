# Upgrade from 5.0 to 5.1

Diem 5.1 is NOT compatible with Diem 5.0.
Here are the main changes and steps to upgrade your projects.

## Run doctrine migrations

php symfony doctrine:generate-migrations-diff
php symfony doctrine:migrate

## Update the project

php symfony dm:setup

This task will upgrade your project as much as possible to work with Diem 5.1.

## Upgrade the layout

With Diem 5.1 the layout can have more than top, left, right and bottom areas.
You can use whatever area name, and as many areas you want to.
The same way, each page as now many areas instead of only "content".
See the new default layout file: dmFrontPlugin/modules/dmFront/templates/pageSuccess.php

## Upgrade your base form filter class
Verify your abstract class BaseFormFilterDoctrine extends dmFormFilterDoctrine
in lib/filter/doctrine/BaseFormFilterDoctrine.class.php