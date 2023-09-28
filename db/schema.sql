CREATE TABLE "files" (
                         "hash" text NOT NULL,
                         "ext" text NOT NULL,
                         "status" text NOT NULL DEFAULT 'uploaded',
                         "is_running" integer NOT NULL DEFAULT '0',
                         "timestamp" integer NOT NULL,
                         PRIMARY KEY ("hash")
);
