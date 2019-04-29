<template>
    <div class="w-full sm:w-1/2 lg:w-1/3 rounded shadow">
        <h2 class="bg-yellow-dark text-sm py-2 px-4 font-hairline font-mono text-yellow-darker">Tasks</h2>
        <ul class="list-reset  px-4 py-4 font-serif bg-yellow-light h-48 overflow-y-scroll scrolling-touch">
            <li v-for="(task, index) in tasks" class="flex">
                <label class="flex w-5/6 flex-start py-1 block text-grey-darkest font-bold cursor-pointer">
                    <input class="mr-2 cursor-pointer"
                            type="checkbox"
                            :dusk="`check-task${task.id}`"
                            :checked="checked(task)"
                            @click="completeTask(task)"
                    >
                    <span :class="[{'line-through' : task.is_completed}, 'text-sm italic font-normal']">
                        {{ task.text }}
                    </span>
                </label>
                <span class="flex-1 cursor-pointer text-center rounded-full px-3 text-yellow-light hover:text-yellow-darker text-xs py-1"
                        @click="removeTask(index, task)"
                        :dusk="`remove-task${task.id}`"
                >✖</span>
            </li>
        </ul>
        <form class="w-full text-sm" @submit.prevent="createTask">
            <div class="flex items-center bg-yellow-lighter py-2">
                <input class="appearance-none bg-transparent border-none w-3/4 text-yellow-darkest mr-3 py-1 px-2 font-serif italic"
                       type="text"
                       placeholder="New Task"
                       aria-label="New Task"
                       v-model="newTask"
                       dusk="task-input"
                >
                <button class="flex-no-shrink bg-yellow hover:bg-yellow font-base font-normal text-yellow-darker py-2 px-4 rounded"
                        type="button"
                        dusk="task-submit"
                        @click="createTask"
                >
                    Add
                </button>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        props: ['initialTasks'],
        data() {
            return {
                newTask: '',
                tasks: this.initialTasks
            }
        },
        methods: {
            createTask(event) {
                if (this.newTask.trim().length === 0) {
                    return;
                }
                axios.post('/api/tasks', {
                    text: this.newTask
                }).then((response) => {
                    this.tasks.push(response.data);
                    this.newTask = '';
                }).catch((e) => console.error(e));
            },
            completeTask(task) {
                let status = ! task.is_completed;
                axios.put(`/api/tasks/${task.id}`, {
                    is_completed: status
                }).then((response) => {
                    task.is_completed = response.data.is_completed
                }).catch((e) => console.error(e));
            },
            checked(task) {
                return task.is_completed;
            },
            removeTask(index, task) {
                axios.delete(`/api/tasks/${task.id}`)
                    .then((response) => {
                        this.tasks = [
                            this.tasks.slice(0, index),
                            this.tasks.slice(index + 1)
                        ];
                    }).catch((e) => console.error(e));
            }
        }
    }
</script>
