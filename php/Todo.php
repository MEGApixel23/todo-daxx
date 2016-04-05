<?php

class Todo
{
    public function actionList()
    {
        return Task::all();
    }

    public function actionView($id)
    {
        return Task::findById($id);
    }

    public function actionAdd($data)
    {
        $task = new Task();

        if (isset($data['checked']))
            $task->setChecked($data['checked']);
        if (isset($data['text']))
            $task->setText($data['text']);

        if ($task->save())
            return $task;

        return false;
    }

    public function actionUpdate($id, $data)
    {
        $task = Task::findById($id);

        if (!$task)
            return false;

        if (isset($data['text']))
            $task->setText($data['text']);
        if (isset($data['checked']))
            $task->setChecked($data['checked']);

        if ($task->save())
            return $task;

        return false;
    }

    public function actionDelete($id)
    {
        $task = Task::findById($id);

        if (!$task)
            return false;

        if (isset($data['text']))
            $task->setText($data['text']);
        if (isset($data['checked']))
            $task->setChecked($data['checked']);

        return $task->delete();
    }
}

/**
 * Class Task
 */
class Task implements \JsonSerializable
{
    private $id;
    private $text;
    private $checked;

    private static $tasks = null;

    /**
     * @return bool
     */
    public function getChecked()
    {
        return $this->checked;
    }

    /**
     * @param bool $checked
     */
    public function setChecked($checked)
    {
        $this->checked = (bool) $checked;
    }

    /**
     * @return string|null
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = (string) $text;
    }

    /**
     * @return integer|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array|null
     */
    public static function all()
    {
        if (self::$tasks === null) {
            $task = new Task();
            $data = $task->getData();

            self::$tasks = $data ?: [];
        }

        return self::$tasks;
    }

    /**
     * @param $id
     * @return null|Task
     */
    public static function findById($id)
    {
        $tasks = static::all();

        foreach ($tasks as $task) {
            if ($task->id === $id) {
                return static::map($task);
            }
        }

        return null;
    }

    /**
     * @param $data
     * @return static
     */
    private static function map($data)
    {
        $task = new static();

        $task->id = $data->id;
        $task->text = $data->text;
        $task->checked = $data->checked;

        return $task;
    }

    /**
     * @return bool
     */
    public function save()
    {
        $tasks = static::all();

        if ($this->id === null) {
            if (empty($tasks))
                $this->id = 1;
            else {
                $lastTask = $tasks[count($tasks) - 1];
                $this->id = $lastTask->id + 1;
            }

            $tasks[] = $this;
        } else {
            foreach ($tasks as &$task) {
                if ($task->id === $this->id) {
                    $task = $this;
                }
            }
        }

        self::$tasks = $tasks;
        return (bool) $this->setData(self::$tasks);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $tasks = static::all();

        for ($i = 0; $i < count($tasks); $i++) {
            if ($tasks[$i]->id === $this->id) {
                unset($tasks[$i]);
                break;
            }
        }

        self::$tasks = array_values($tasks);
        return (bool) $this->setData(self::$tasks);
    }

    /**
     * @return bool|mixed
     */
    private function getData()
    {
        $path = $this->getStoragePath();
        $data = file_get_contents($path);

        if ($data === false)
            return false;

        return json_decode($data);
    }

    /**
     * @param $info
     * @return int
     */
    private function setData($info)
    {
        $path = $this->getStoragePath();
        return file_put_contents($path, json_encode($info));
    }

    /**
     * @return string
     */
    private function getStoragePath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'data.json';
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => (int) $this->getId(),
            'text' => (string) $this->getText(),
            'checked' => (boolean) $this->getChecked(),
        ];
    }
}