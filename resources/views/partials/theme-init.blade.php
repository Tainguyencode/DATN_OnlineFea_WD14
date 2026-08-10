<script>
    (() => {
        const root = document.documentElement;
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia?.('(prefers-color-scheme: dark)')?.matches ?? false;

        root.classList.toggle('dark', savedTheme === 'dark' || (!savedTheme && prefersDark));
    })();
</script>
