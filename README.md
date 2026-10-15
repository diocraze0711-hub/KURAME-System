/* Additional UI adjustments for images, reactions, replies, notes */

.post-images { display:flex; gap:8px; margin-top:12px; flex-wrap:wrap; }
.post-image { width:160px; height:120px; object-fit:cover; border-radius:8px; border:1px solid rgba(255,255,255,0.06); }
.post-actions { display:flex; gap:8px; margin-top:12px; }
.react-btn { padding:8px 10px; border-radius:8px; background:rgba(79,70,229,0.12); border:1px solid rgba(255,255,255,0.04); }
.message-attachments { display:flex; gap:8px; margin-top:10px; }
.message-image { width:120px; height:90px; object-fit:cover; border-radius:6px; }
.comment-actions { display:flex; gap:8px; align-items:center; margin-top:8px; }
.reply-item { padding:8px; background:rgba(9,17,31,0.6); border-radius:8px; margin-top:8px; }
.note-block { margin-top:12px; border-top:1px dashed rgba(255,255,255,0.04); padding-top:10px; }
.note-item { background: rgba(9,17,31,0.6); padding:8px; border-radius:8px; margin-bottom:8px; }

/* Layout tweaks to reduce clutter */
.post-card { padding:16px; }
.comment-item { padding:10px; border-radius:10px; background: rgba(11,20,34,0.65); }

/* Responsive */
@media (max-width:900px) {
    .post-image { width:100px; height:80px; }
    .message-image { width:90px; height:70px; }
}
