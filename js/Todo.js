function Todo() {
    var $taskItemLayout = $('.layouts .task-item');
    var $tasksContainer = $('.tasks-container'),
        $newTask = $tasksContainer.find('.new-task-item');

    this.getTasks = function () {
        var url = '/php/ajax.php?action=list';

        $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: function(res) {
                if (res.length > 0) {
                    $(res).each(function() {
                        insertNewTask(this);
                    });
                }
            },
            error: function(res) {}
        });
    };

    this.addTask = function () {
        var url = '/php/ajax.php?action=add';

        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            success: function(res) {
                insertNewTask(res);
            },
            error: function() {
                alert('Something is wrong');
            }
        });
    };

    this.deleteTask = function (id) {
        var url = '/php/ajax.php?action=delete&id=' + id;

        $.ajax({
            type: 'DELETE',
            url: url,
            dataType: 'json',
            success: function() {
                removeTaskById(id);
            },
            error: function() {
                alert('Something is wrong');
            }
        });
    };

    this.updateTask = function (id, data) {
        var url = '/php/ajax.php?action=update&id=' + id;

        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            data: data,
            error: function() {
                alert('Something is wrong');
            }
        });
    };

    function insertNewTask(taskData) {
        var $layout = $taskItemLayout.clone();

        $layout.find('[data-property=id]').attr({'data-id': taskData.id});
        $layout.find('[data-property=text]').attr({'value': taskData.text});

        if (taskData.checked === true)
            $layout.find('[data-property=checked]').attr({checked: 'checked'});

        $newTask.before($layout);
    }

    function removeTaskById(id) {
        var $task = $('[data-property=id][data-id=' + id + ']');

        if (!$task.length)
            return false;

        $task.closest('.task-item').remove();
    }

    return this;
}

var todo = new Todo();
todo.getTasks();

$('.new-task-item').click(function(e) {
    e.preventDefault();

    todo.addTask();
});

$('body')
    // Delete task event handler
    .off('click', '.delete-task')
    .on('click', '.delete-task', function(e) {
        e.preventDefault();
        var id = parseInt($(this).attr('data-id'));

        if (isNaN(id))
            return false;

        todo.deleteTask(id);
    })

    // Change task's text event handler
    .off('keypress', '[data-property=text]')
    .on('keypress', '[data-property=text]', function(e) {
        var enterCode = 13;

        if (e.keyCode === enterCode) {
            $(this).blur();
        }

        return true;
    })

    // Change task's text event handler (whet input loses focus)
    .off('blur', '[data-property=text]')
    .on('blur', '[data-property=text]', function(e) {
        var text = $(this).val(),
            $taskContainer = $(this).closest('.task-item'),
            id = parseInt($taskContainer.find('[data-property=id]').attr('data-id'));

        if (isNaN(id))
            return false;

        todo.updateTask(id, {text: text});
    })

    // Change task's checked state event handler
    .off('change', '[data-property=checked]')
    .on('change', '[data-property=checked]', function(e) {
        var checked = $(this).is(':checked'),
            $taskContainer = $(this).closest('.task-item'),
            id = parseInt($taskContainer.find('[data-property=id]').attr('data-id'));

        if (isNaN(id))
            return false;

        todo.updateTask(id, {checked: checked});
    });