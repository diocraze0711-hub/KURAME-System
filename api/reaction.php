document.addEventListener('DOMContentLoaded', function () {
    // disable submit buttons on submit
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function () {
            const button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
                button.textContent = 'Processing...';
            }
        });
    });

    // reaction buttons (AJAX)
    document.querySelectorAll('.react-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const entity = btn.getAttribute('data-entity');
            const id = btn.getAttribute('data-id');
            const reaction = btn.getAttribute('data-reaction');

            fetch('api/reaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ entity_type: entity, entity_id: id, reaction_type: reaction })
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    btn.textContent = reaction + (data.status === 'added' ? ' ✓' : '');
                } else {
                    alert('Reaction failed');
                }
            }).catch(err => {
                console.error(err);
                alert('Reaction error');
            });
        });
    });

    // reply toggles
    document.querySelectorAll('.reply-toggle').forEach(rt => {
        rt.addEventListener('click', function () {
            const id = rt.getAttribute('data-comment-id');
            const form = document.getElementById('reply-form-' + id);
            if (form) {
                form.style.display = form.style.display === 'none' ? 'block' : 'none';
            }
        });
    });

    // save notes (AJAX)
    document.querySelectorAll('.save-note').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = btn.closest('.note-form');
            const targetType = form.getAttribute('data-target-type');
            const targetId = form.getAttribute('data-target-id');
            const note = form.querySelector('textarea[name="note"]').value;

            fetch('api/note.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ target_type: targetType, target_id: targetId, note: note })
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    // append note to list
                    const list = document.querySelector('.notes-list[data-notes-for="' + targetType + '-' + targetId + '"]');
                    if (list) {
                        const el = document.createElement('div');
                        el.className = 'note-item';
                        el.innerHTML = '<small>Just now</small><p>' + (note.length > 200 ? note.substring(0,200)+'...' : note) + '</p>';
                        list.prepend(el);
                        form.querySelector('textarea[name="note"]').value = '';
                    }
                } else {
                    alert('Could not save note');
                }
            }).catch(err => {
                console.error(err);
                alert('Note error');
            });
        });
    });

});
