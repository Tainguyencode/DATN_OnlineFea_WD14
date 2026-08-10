<script>
    (() => {
        const root = document.documentElement;
        const useSystemPreference = @json($useSystemPreference ?? true);
        let savedTheme = null;

        try {
            savedTheme = localStorage.getItem('theme');
        } catch (error) {
            // Storage can be unavailable in strict privacy contexts.
        }

        const prefersDark = window.matchMedia?.('(prefers-color-scheme: dark)')?.matches ?? false;

        root.classList.toggle('dark', savedTheme === 'dark' || (!savedTheme && useSystemPreference && prefersDark));
    })();
</script>
