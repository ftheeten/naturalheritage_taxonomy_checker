-- Table: public.taxon_checker

-- DROP TABLE IF EXISTS public.taxon_checker;

CREATE TABLE IF NOT EXISTS public.taxon_checker
(
    id integer NOT NULL DEFAULT nextval('taxon_checker_id_seq'::regclass),
    session character varying COLLATE pg_catalog."default",
    data json,
    date_data timestamp with time zone,
    mail character varying COLLATE pg_catalog."default",
    CONSTRAINT pk_service PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.taxon_checker
    OWNER to postgres;



GRANT ALL ON TABLE public.taxon_checker TO postgres;