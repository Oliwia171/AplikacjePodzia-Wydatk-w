<button
    type="button"
    x-data="themeToggle"
    x-on:click="toggle()"
    class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800 dark:focus:ring-offset-gray-900"
    x-bind:aria-label="theme === 'dark' ? 'Przelacz na tryb jasny' : 'Przelacz na tryb ciemny'"
>
    <span x-text="theme === 'dark' ? 'Tryb jasny' : 'Tryb ciemny'"></span>
</button>
