CREATE TABLE transactions
(
    id                 VARCHAR(255) NOT NULL,
    external_id        VARCHAR(255) NOT NULL,
    amount_minor_units BIGINT       NOT NULL,
    currency           CHAR(3)      NOT NULL,
    occurred_at        DATETIME     NOT NULL,
    status             VARCHAR(32)  NOT NULL,
    created_at         DATETIME     NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY transactions_external_id_unique (external_id),
    CONSTRAINT transactions_currency_length CHECK (CHAR_LENGTH(currency) = 3),
    INDEX transactions_occurred_at_index (occurred_at),
    INDEX transactions_status_index (status)
);
