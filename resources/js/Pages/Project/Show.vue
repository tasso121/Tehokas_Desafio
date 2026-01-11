<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({ project: Object });

const form = useForm({
    project_id: props.project.id,
    title: '',
    description: '',
    status: 'pending',
    priority: 'medium',
    deadline: '',
});

const submitTask = () => {
    form.post(route('tasks.store'), {
        onSuccess: () => {
            form.reset('title', 'description', 'deadline');
        },
    });
};

const updateStatus = (task, newStatus) => {
    router.patch(route('tasks.update', task.id), {
        status: newStatus
    }, {
        preserveScroll: true
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

const tasksByStatus = computed(() => {
    const filter = (status) => {
        return props.project.tasks.filter(t => {
            const statusMatch = t.status === status;
            const priorityMatch = filterPriority.value === 'all' || t.priority === filterPriority.value;
            return statusMatch && priorityMatch;
        });
    };

    return {
        pending: filter('pending'),
        in_progress: filter('in_progress'),
        completed: filter('completed'),
    };
});

const isOverdue = (task) => {
    return task.status !== 'completed' && new Date(task.deadline) < new Date();
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const statusLabels = {
    pending: 'Pendente',
    in_progress: 'Em Andamento',
    completed: 'Concluída'
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
                    <div class="flex items-center">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Filtrar Prioridade:</label>
                        <select v-model="filterPriority" class="text-sm rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white py-1">
                            <option value="all">Todas</option>
                            <option value="high">Alta</option>
                            <option value="medium">Média</option>
                            <option value="low">Baixa</option>
                        </select>
                    </div>

                    <span v-if="project.is_on_alert" class="px-3 py-1 text-sm font-bold text-red-100 bg-red-600 rounded-full animate-pulse">
                        ⚠️ Projeto Em Alerta
                    </span>
                    <span v-else class="px-3 py-1 text-sm font-bold text-green-100 bg-green-600 rounded-full">
                        ✅ Projeto Saudável
                    </span>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Add Task Form -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-8 p-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Adicionar Nova Tarefa</h3>
                    <form @submit.prevent="submitTask" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                            <input v-model="form.title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" required>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prazo (Deadline)</label>
                            <input v-model="form.deadline" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" required>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Inicial</label>
                            <select v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                <option value="pending">Pendente</option>
                                <option value="in_progress">Em Andamento</option>
                                <option value="completed">Concluída</option>
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridade</label>
                            <select v-model="form.priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" required>
                                <option value="low">Baixa</option>
                                <option value="medium">Média</option>
                                <option value="high">Alta</option>
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <button type="submit" :disabled="form.processing" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none disabled:opacity-50">
                                Adicionar
                            </button>
                        </div>
                        <div class="md:col-span-5 mt-2">
                             <input v-model="form.description" type="text" placeholder="Detalhes da tarefa (opcional)..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </form>
                </div>

                <!-- Kanban Board -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Column: Pendente -->
                    <div class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg min-h-[500px]">
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 mb-4 flex justify-between">
                            PENDENTE 
                            <span class="bg-gray-200 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ tasksByStatus.pending.length }}</span>
                        </h3>
                        <div v-for="task in tasksByStatus.pending" :key="task.id" class="bg-white dark:bg-gray-800 p-4 rounded shadow mb-3 border-l-4 border-yellow-400 group relative">
                             <div class="flex justify-between items-start">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ task.title }}</h4>
                                <button @click="deleteTask(task)" class="text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                    &times;
                                </button>
                             </div>
                             <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ task.description }}</p>
                             <div class="mt-3 flex justify-between items-center text-xs">
                                <span class="px-2 py-0.5 rounded border text-[10px] uppercase font-bold" :class="getPriorityBadgeClass(task.priority)">
                                    {{ getPriorityLabel(task.priority) }}
                                </span>
                                <span :class="{'text-red-500 font-bold': isOverdue(task), 'text-gray-500': !isOverdue(task)}">
                                    📅 {{ formatDate(task.deadline) }}
                                </span>
                             </div>
                             
                             <!-- Actions -->
                             <div class="mt-3 pt-3 border-t dark:border-gray-700 flex justify-end">
                                <button @click="updateStatus(task, 'in_progress')" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200">
                                    Iniciar &rarr;
                                </button>
                             </div>
                        </div>
                    </div>

                    <!-- Column: Em Andamento -->
                    <div class="bg-blue-50 dark:bg-gray-900 p-4 rounded-lg min-h-[500px]">
                        <h3 class="font-bold text-blue-700 dark:text-blue-300 mb-4 flex justify-between">
                            EM ANDAMENTO
                             <span class="bg-blue-200 text-blue-800 text-xs px-2 py-0.5 rounded-full">{{ tasksByStatus.in_progress.length }}</span>
                        </h3>
                        <div v-for="task in tasksByStatus.in_progress" :key="task.id" class="bg-white dark:bg-gray-800 p-4 rounded shadow mb-3 border-l-4 border-blue-500 group relative">
                             <div class="flex justify-between items-start">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ task.title }}</h4>
                                <button @click="deleteTask(task)" class="text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                    &times;
                                </button>
                             </div>
                             <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ task.description }}</p>
                             <div class="mt-3 flex justify-between items-center text-xs">
                                <span class="px-2 py-0.5 rounded border text-[10px] uppercase font-bold" :class="getPriorityBadgeClass(task.priority)">
                                    {{ getPriorityLabel(task.priority) }}
                                </span>
                                <span :class="{'text-red-500 font-bold': isOverdue(task), 'text-gray-500': !isOverdue(task)}">
                                    📅 {{ formatDate(task.deadline) }}
                                </span>
                             </div>

                             <!-- Actions -->
                             <div class="mt-3 pt-3 border-t dark:border-gray-700 flex justify-between">
                                <button @click="updateStatus(task, 'pending')" class="text-xs text-gray-500 hover:text-gray-700">
                                    &larr; Voltar
                                </button>
                                <button @click="updateStatus(task, 'completed')" class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200">
                                    Concluir &rarr;
                                </button>
                             </div>
                        </div>
                    </div>

                    <!-- Column: Concluída -->
                    <div class="bg-green-50 dark:bg-gray-900 p-4 rounded-lg min-h-[500px]">
                        <h3 class="font-bold text-green-700 dark:text-green-300 mb-4 flex justify-between">
                            CONCLUÍDA
                             <span class="bg-green-200 text-green-800 text-xs px-2 py-0.5 rounded-full">{{ tasksByStatus.completed.length }}</span>
                        </h3>
                         <div v-for="task in tasksByStatus.completed" :key="task.id" class="bg-white dark:bg-gray-800 p-4 rounded shadow mb-3 border-l-4 border-green-500 opacity-75 group relative">
                             <div class="flex justify-between items-start">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 line-through text-gray-500">{{ task.title }}</h4>
                                <button @click="deleteTask(task)" class="text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                    &times;
                                </button>
                             </div>
                             <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ task.description }}</p>
                             
                             <!-- Actions -->
                             <div class="mt-3 pt-3 border-t dark:border-gray-700 flex justify-start">
                                <button @click="updateStatus(task, 'in_progress')" class="text-xs text-gray-500 hover:text-gray-700">
                                    &larr; Reabrir
                                </button>
                             </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
