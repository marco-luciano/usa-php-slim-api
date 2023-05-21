CREATE TABLE IF NOT EXISTS states (
    state_id serial NOT NULL,
    name varchar UNIQUE NOT NULL,
    abbreviation char(2) CHECK (abbreviation ~ '^[A-Z]{2}$') UNIQUE NOT NULL, --two capital letters only
    date_add timestamptz DEFAULT NOW(),
    date_upd timestamptz DEFAULT NOW(),
    PRIMARY KEY (state_id)
);


CREATE TABLE IF NOT EXISTS counties (
    county_id serial NOT NULL,
    name varchar NOT NULL,
    state_id integer NOT NULL,
    population integer CHECK (population >= 0) NOT NULL,
    date_add timestamptz DEFAULT NOW(),
    date_upd timestamptz DEFAULT NOW(),
    PRIMARY KEY (county_id)
);

CREATE TABLE IF NOT EXISTS users (
    user_id serial NOT NULL,
    name varchar UNIQUE NOT NULL,
    password varchar NOT NULL,
    date_add timestamptz DEFAULT NOW(),
    date_upd timestamptz DEFAULT NOW(),
    PRIMARY KEY (user_id)
);

