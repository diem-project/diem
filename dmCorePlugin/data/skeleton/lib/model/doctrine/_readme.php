<?php return ?>

What's this file ?!

## PROBLEM
With symfony and Doctrine, you can't generate migrations if your schema.yml is empty.
This has nothing to do with Diem.

So the first time you add tables in your schema, you have to create the database from scratch.
This is bad because plugins have also tables, and they will be dropped and created again.
And your data may be lost.

##SOLUTION
The less ugly way to allow migrations with an empty schema.yml is
to have at least one php file in /libs/model/doctrine.
The migration task finds it and allows the migration generation.

This is the file.
You can safely remove it once your project has php files in this folder.
You may also let it here.