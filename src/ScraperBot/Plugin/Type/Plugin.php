<?php


namespace ScraperBot\Plugin\Type;


class Plugin {

    private $id;
    private $class;
    private $description;

    public function __construct($id, $description, $class) {
        $this->setId($id);
        $this->setDescription($description);
        $this->setClass($class);
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class): void {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void {
        $this->description = $description;
    }

}
