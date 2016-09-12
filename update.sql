ALTER TABLE api_race DROP CONSTRAINT api_race_category_check;
ALTER TABLE api_race ADD CHECK(category IN ('ultra_marathon', 'marathon', 'half_marathon', '5k', '10k'));
