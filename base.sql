CREATE TABLE IF NOT EXISTS `status_pedido` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `status` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(45) NULL DEFAULT NULL,
  `preco` DECIMAL NULL DEFAULT NULL,
  `status` INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  INDEX `fk_pedidos_status_pedido_idx` (`status` ASC),
  CONSTRAINT `fk_pedidos_status_pedido`
    FOREIGN KEY (`status`)
    REFERENCES `status_pedido` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;