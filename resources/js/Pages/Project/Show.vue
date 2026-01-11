<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({ project: Object });

const showTaskModal = ref(false);
const editingTask = ref(null);
const showColumnForm = ref(false);

const taskForm = useForm({
    project_id: props.project.id,
    column_id: '',
    title: '',
    description: '',
    priority: 'medium',
    deadline: '',
});

const columnForm = useForm({
    name: '',
});

const openTaskModal = (task = null) => {
    editingTask.value = task;
    if (task) {
        taskForm.column_id = task.column_id;
        taskForm.title = task.title;
        taskForm.description = task.description;
        taskForm.priority = task.priority;
        taskForm.deadline = task.deadline ? new Date(task.deadline).toISOString().slice(0, 16) : '';
    } else {
        taskForm.reset();
        taskForm.column_id = props.project.columns[0]?.id || '';
        taskForm.project_id = props.project.id;
        taskForm.priority = 'medium';
    }
    showTaskModal.value = true;
};

const closeTaskModal = () => {
    showTaskModal.value = false;
    editingTask.value = null;
    taskForm.reset();
};

const submitTask = () => {
    if (editingTask.value) {
        taskForm.patch(route('tasks.update', editingTask.value.id), {
            onSuccess: closeTaskModal,
        });
    } else {
        taskForm.post(route('tasks.store'), {
            onSuccess: closeTaskModal,
        });
    }
};

const submitColumn = () => {
    columnForm.post(route('columns.store', props.project.id), {
        onSuccess: () => {
            columnForm.reset();
            showColumnForm.value = false;
        }
    });
};

const deleteTask = (task) => {
    if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
        router.delete(route('tasks.destroy', task.id), {
            preserveScroll: true
        });
    }
};

const filterPriority = ref('all');

const filteredColumns = computed(() => {
    return props.project.columns.map(column => {
        const filteredTasks = column.tasks.filter(t => {
            const priorityMatch = filterPriority.value === 'all' || t.priority === filterPriority.value;
            return priorityMatch;
        });
        return {
            ...column,
            tasks: filteredTasks
        };
    });
});

const isOverdue = (task) => {
    if (!task.deadline) return false;
    return false;
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const getPriorityBadgeClass = (priority) => {
    switch (priority) {
        case 'high': return 'bg-red-100 text-red-800 border-red-200';
        case 'medium': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        case 'low': return 'bg-gray-100 text-gray-800 border-gray-200';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const getPriorityLabel = (priority) => {
    switch (priority) {
        case 'high': return 'Alta';
        case 'medium': return 'Média';
        case 'low': return 'Baixa';
        default: return priority;
    }
};

const moveTask = (task, targetColumnId) => {
    router.patch(route('tasks.update', task.id), {
        column_id: targetColumnId
    }, { preserveScroll: true });
};
</script>

<template>
    <Head :title="project.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        <Link :href="route('dashboard')" class="text-indigo-500 hover:underline">Projetos</Link>
                        <span class="text-gray-400 mx-2">/</span>
                        {{ project.name }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">{{ project.description }}</p>
                </div>
                <div class="flex items-center space-x-4">
                     <!-- Filter -->
                    <div class="flex items-center">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Prioridade:</label>
                        <select v-model="filterPriority" class="text-sm rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white py-1">
                            <option value="all">Todas</option>
                            <option value="high">Alta</option>
                            <option value="medium">Média</option>
                            <option value="low">Baixa</option>
                        </select>
                    </div>

                    <span v-if="project.is_on_alert" class="px-3 py-1 text-sm font-bold text-red-100 bg-red-600 rounded-full animate-pulse">
                        ⚠️ Alerta
                    </span>
                    <span v-else class="px-3 py-1 text-sm font-bold text-green-100 bg-green-600 rounded-full">
                        ✅ Saudável
                    </span>
                    
                    <!-- Add Task Button -->
                    <button @click="openTaskModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow">
                        + Nova Tarefa
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6 overflow-x-auto">
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                
                <!-- Kanban Board -->
                <div class="flex space-x-4 items-start min-h-[calc(100vh-200px)]">
                    
                    <!-- Dynamic Columns -->
                    <div v-for="(column, index) in filteredColumns" :key="column.id" class="w-80 flex-shrink-0 bg-gray-100 dark:bg-gray-900 p-4 rounded-lg flex flex-col max-h-[80vh]">
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 mb-4 flex justify-between uppercase">
                            {{ column.name }}
                            <span class="bg-gray-200 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ column.tasks.length }}</span>
                        </h3>

                        <div class="overflow-y-auto flex-1 pr-2 space-y-3">
                            <div v-for="task in column.tasks" :key="task.id" 
                                class="bg-white dark:bg-gray-800 p-3 rounded shadow border-l-4 group relative hover:shadow-md transition-all"
                                :class="column.is_completed ? 'border-green-500 opacity-75' : 'border-indigo-500'"
                            >
                                <div class="flex justify-between items-start">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm" :class="{'line-through text-gray-500': column.is_completed}">{{ task.title }}</h4>
                                    
                                    <div class="flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <!-- Edit Button -->
                                        <button @click="openTaskModal(task)" class="text-blue-400 hover:text-blue-600">
                                            ✏️
                                        </button>
                                        <!-- Delete Button -->
                                        <button @click="deleteTask(task)" class="text-gray-400 hover:text-red-500">
                                            &times;
                                        </button>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ task.description }}</p>
                                
                                <div class="mt-2 flex justify-between items-center text-xs">
                                     <span class="px-2 py-0.5 rounded border text-[10px] uppercase font-bold" :class="getPriorityBadgeClass(task.priority)">
                                        {{ getPriorityLabel(task.priority) }}
                                    </span>
                                    
                                    <!-- Overdue Logic Check inline -->
                                    <span :class="{
                                        'text-red-500 font-bold': !column.is_completed && new Date(task.deadline) < new Date(),
                                        'text-gray-500': column.is_completed || new Date(task.deadline) >= new Date()
                                    }">
                                        📅 {{ formatDate(task.deadline) }}
                                    </span>
                                </div>

                                <!-- Move Actions (Simple Logic) -->
                                <div class="mt-2 pt-2 border-t dark:border-gray-700 flex justify-between" v-if="filteredColumns.length > 1">
                                    <button v-if="index > 0" @click="moveTask(task, filteredColumns[index - 1].id)" class="text-xs text-gray-500 hover:text-gray-700">
                                        &larr;
                                    </button>
                                    <span v-else></span>

                                    <button v-if="index < filteredColumns.length - 1" @click="moveTask(task, filteredColumns[index + 1].id)" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold">
                                        &rarr;
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Column Column -->
                    <div class="w-80 flex-shrink-0">
                        <div v-if="!showColumnForm" @click="showColumnForm = true" class="bg-gray-100/50 dark:bg-gray-900/50 p-4 rounded-lg border-2 border-dashed border-gray-300 hover:border-indigo-500 cursor-pointer flex items-center justify-center h-16 text-gray-500 hover:text-indigo-500 transition-colors">
                            + Adicionar Coluna
                        </div>
                        <div v-else class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                            <form @submit.prevent="submitColumn">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome da Coluna</label>
                                <input v-model="columnForm.name" type="text" class="block w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" placeholder="ex: Revisão" required ref="colInput">
                                <div class="mt-3 flex space-x-2">
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-sm w-full">Salvar</button>
                                    <button type="button" @click="showColumnForm = false" class="text-gray-500 hover:text-gray-700 px-2 py-1.5 rounded text-sm">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Task Modal -->
        <Modal :show="showTaskModal" @close="closeTaskModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ editingTask ? 'Editar Tarefa' : 'Nova Tarefa' }}
                </h2>

                <form @submit.prevent="submitTask" class="mt-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                        <input v-model="taskForm.title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Coluna / Status</label>
                        <select v-model="taskForm.column_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" required>
                            <option v-for="col in props.project.columns" :key="col.id" :value="col.id">{{ col.name }}</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridade</label>
                            <select v-model="taskForm.priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                <option value="low">Baixa</option>
                                <option value="medium">Média</option>
                                <option value="high">Alta</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prazo</label>
                            <input v-model="taskForm.deadline" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
                        <textarea v-model="taskForm.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="button" @click="closeTaskModal" class="mr-3 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="taskForm.processing" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ editingTask ? 'Salvar Alterações' : 'Criar Tarefa' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
