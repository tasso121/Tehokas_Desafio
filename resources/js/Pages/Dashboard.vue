<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({ projects: Array });

const form = useForm({
    name: '',
    description: '',
});

const submit = () => {
    form.post(route('projects.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Create Project Form -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Novo Projeto</h3>
                    <form @submit.prevent="submit" class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome do Projeto</label>
                            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required placeholder="Ex: Website Redesign">
                        </div>
                        <div class="flex-1 w-full">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
                            <input v-model="form.description" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Breve descrição...">
                        </div>
                        <button type="submit" :disabled="form.processing" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                            Criar
                        </button>
                    </form>
                </div>

                <!-- Projects List -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="project in projects" :key="project.id" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow border-l-4" :class="project.is_on_alert ? 'border-red-500' : 'border-green-500'">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white truncate" :title="project.name">{{ project.name }}</h3>
                                <div class="shrink-0 ml-2">
                                    <span v-if="project.is_on_alert" class="px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">Em Alerta</span>
                                    <span v-else class="px-2 py-1 text-xs font-bold leading-none text-green-100 bg-green-600 rounded-full">Saudável</span>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 mb-4 h-12 overflow-hidden text-ellipsis">{{ project.description || 'Sem descrição.' }}</p>
                            <div class="flex justify-end">
                                <Link :href="route('projects.show', project.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium inline-flex items-center">
                                    Ver Kanban <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-if="projects.length === 0" class="text-center text-gray-500 dark:text-gray-400 mt-12 bg-white dark:bg-gray-800 p-8 rounded-lg shadow">
                    <p class="text-lg">Nenhum projeto encontrado.</p>
                    <p class="text-sm">Utilize o formulário acima para criar seu primeiro projeto.</p>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
