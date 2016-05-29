ALTER TABLE api_race DROP CONSTRAINT api_race_distance_check;
ALTER TABLE api_race ALTER distance DROP DEFAULT;
ALTER TABLE api_race ALTER COLUMN distance TYPE integer USING (distance::integer);
