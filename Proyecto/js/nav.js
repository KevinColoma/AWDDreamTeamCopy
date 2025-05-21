        document.querySelectorAll('.nav-item[data-target]').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');

                const targetFile = item.getAttribute('data-target');
                const iframe = document.getElementById('content-frame');
                iframe.src = targetFile;
            });
        });

        document.querySelector('.sidebar-toggle').addEventListener('click', () => {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
            if (sidebar.classList.contains('active')) {
                sidebar.style.animation = 'slideIn 0.5s ease-out';
            } else {
                sidebar.style.animation = 'slideOut 0.5s ease-out';
            }
        });

        window.addEventListener('resize', () => {
            const iframe = document.getElementById('content-frame');
            iframe.style.height = `${window.innerHeight}px`;
        });