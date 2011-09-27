<?

chdir(__DIR__);

passthru('rm data/*.sqlite');
passthru('php symfony doctrine:drop-db --env=test --no-confirmation');
passthru('php symfony doctrine:create-db --env=test');
passthru('php symfony doctrine:insert-sql --env=test');
passthru('php symfony dm:setup -n --env=test');
passthru('php symfony dm:data --env=test');
passthru('php symfony my:project-builder --env=test');
copy('data/db.sqlite', 'data/fresh_db.sqlite');
