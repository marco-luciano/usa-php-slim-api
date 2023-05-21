#!/bin/bash

# PostgreSQL connection details

if [[ $POSTGRES_HOST ]]
then 
    DB_HOST=$POSTGRES_HOST
else
    DB_HOST="db"
fi

STATES_CSV_FILE="/scripts/db_data_upload/states.csv"
COUNTIES_CSV_FILE="/scripts/db_data_upload/counties.csv"

# SQL command to create temporary table
CREATE_TEMP_TABLE_SQL="CREATE TEMPORARY TABLE temp_states (state_id INT, name TEXT, abbreviation VARCHAR(2));
 CREATE TEMPORARY TABLE temp_counties (county_id INT, name TEXT, state_id INT, population INT);"

# SQL command to copy data from CSV into the temporary tables
STATES_COPY_SQL="COPY temp_states (state_id, name, abbreviation) FROM '$STATES_CSV_FILE' WITH (FORMAT CSV, HEADER, DELIMITER ',');"
COUNTIES_COPY_SQL="COPY temp_counties (county_id, name, state_id, population) FROM '$COUNTIES_CSV_FILE' WITH (FORMAT CSV, HEADER, DELIMITER ',');"

# SQL commands to insert data from temporary table into the states and counties tables
STATES_INSERT_SQL="INSERT INTO states (state_id, name, abbreviation) SELECT state_id, name, abbreviation FROM temp_states ON CONFLICT(state_id) DO NOTHING;"
COUNTIES_INSERT_SQL="INSERT INTO counties (county_id, name, state_id, population) SELECT county_id, name, state_id, population FROM temp_counties ON CONFLICT(county_id) DO NOTHING;"

# 3144 counties
SETVAL_SQL="SELECT SETVAL('counties_county_id_seq', 3144);"

ENC_TEST_PASSWD=$(echo -n "$TEST_PASSWORD" | sha3sum -a 512 | cut -d' ' -f1);
TEST_USER_SQL="INSERT INTO users (name, password) VALUES ('test', '$ENC_TEST_PASSWD') ON CONFLICT(name) DO NOTHING;";

export PGPASSWORD="$POSTGRES_PASSWORD"

# Construct the psql command to execute the SQL statements
PSQL_COMMAND="psql -h $DB_HOST -d $POSTGRES_DB -U $POSTGRES_USER -c \"$CREATE_TEMP_TABLE_SQL $STATES_COPY_SQL $COUNTIES_COPY_SQL $STATES_INSERT_SQL $COUNTIES_INSERT_SQL $SETVAL_SQL $TEST_USER_SQL\""

# Execute the psql command
eval $PSQL_COMMAND
