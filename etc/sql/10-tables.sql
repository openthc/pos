

CREATE TABLE b2c_sale (
	id character varying(32) DEFAULT ulid_create() NOT NULL PRIMARY KEY,
	license_id character varying(32) NOT NULL,
	contact_id character varying(32) NOT NULL,
	stat integer DEFAULT 100 NOT NULL,
	flag integer DEFAULT 0 NOT NULL,
	created_at timestamp with time zone DEFAULT now() NOT NULL,
	hash varchar(64),
	guid varchar(64),
	meta jsonb
);


CREATE TABLE b2c_sale_hold (
	id character varying(32) DEFAULT ulid_create() NOT NULL PRIMARY KEY,
	contact_id character varying(32) NOT NULL,
	stat integer DEFAULT 100 NOT NULL,
	created_at timestamp with time zone DEFAULT now() NOT NULL,
	meta jsonb
);


CREATE TABLE b2c_sale_item (
	id character varying(32) DEFAULT ulid_create() NOT NULL PRIMARY KEY,
	b2c_sale_id character varying(32) NOT NULL,
	stat integer DEFAULT 100 NOT NULL,
	flag integer DEFAULT 0 NOT NULL,
	sort integer DEFAULT 0 NOT NULL,
	created_at timestamp with time zone DEFAULT now() NOT NULL,
	hash varchar(64),
	guid varchar(64),
	qty numeric(16, 4) NOT NULL,
	unit_price numeric(12,4),
);
