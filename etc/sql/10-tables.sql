

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
	uom varchar(8),
	unit_price numeric(12,4)
);


CREATE TABLE inventory (
	id bigserial PRIMARY KEY,
	license_id bigint NOT NULL,
	product_id bigint NOT NULL,
	strain_id bigint NOT NULL,
	room_id bigint NOT NULL,
	stat integer NOT NULL,
	flag integer DEFAULT 0 NOT NULL,
	name text,
	cost numeric(16,2),
	sell numeric(16,2),
	qty numeric(16,4),
	qty_initial numeric(16,4),
	unit_weight numeric(16,4),
	qa_cbd varchar(16),
	qa_thc varchar(16),
	hash character varying(64),
	guid character varying(128) NOT NULL,
	created_at timestamp with time zone DEFAULT now() NOT NULL,
	updated_at timestamp with time zone DEFAULT now() NOT NULL,
	deleted_at timestamp with time zone,
	meta jsonb
);
