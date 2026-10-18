/* ==========================================
   MIND CAPSULE — FREEDOM WALL DESIGN
   Blush Pink • Rose • Burgundy
========================================== */

.freedom-wall {
    padding: 45px 20px 60px;
    background: linear-gradient(
        180deg,
        #fff0f4 0%,
        #fff9fb 35%,
        #fff5f8 100%
    );
    min-height: 100vh;
    color: #654352;
}

.fw-container {
    max-width: 1250px;
    margin: 0 auto;
    width: 100%;
}

/* HERO SECTION */

.fw-hero {
    text-align: center;
    padding: 45px 20px;
    margin-bottom: 35px;
}

.fw-symbol {
    display: block;
    color: #c77b98;
    font-size: 21px;
    margin-bottom: 18px;
}

.fw-eyebrow,
.fw-kicker {
    color: #a45a76;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 2.5px;
    margin: 0 0 12px;
}

.fw-hero h1 {
    color: #7f203c;
    font-family: "Playfair Display", Georgia, serif;
    font-size: clamp(34px, 5vw, 58px);
    font-weight: 500;
    line-height: 1.2;
    margin: 12px 0 18px;
}

.fw-hero h1 span {
    color: #c72c50;
    font-style: italic;
}

.fw-intro {
    max-width: 600px;
    margin: 0 auto;
    color: #906b7b;
    font-size: 15px;
    line-height: 1.9;
}

.fw-divider {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    margin-top: 28px;
    color: #b96b87;
    font-size: 10px;
    letter-spacing: 2px;
}

/* MAIN TWO-COLUMN LAYOUT */

.fw-layout {
    display: grid;
    grid-template-columns: minmax(280px, 0.85fr) minmax(0, 1.4fr);
    align-items: start;
    gap: 28px;
}

.fw-compose-column,
.fw-community {
    min-width: 0;
}

/* CARDS */

.fw-card,
.fw-post,
.fw-empty {
    background: rgba(255, 255, 255, 0.96);
    border: 1px solid #efd0db;
    border-radius: 22px;
    box-shadow: 0 12px 35px rgba(127, 32, 60, 0.075);
}

.fw-card {
    padding: 28px;
}

.fw-card-heading {
    display: flex;
    align-items: center;
    gap: 14px;
}

.fw-mini-symbol {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    border-radius: 16px;
    background: #fff0f4;
    color: #bd5276;
    font-size: 24px;
}

.fw-card-heading h2,
.fw-community-heading h2 {
    margin: 0;
    color: #7f203c;
    font-family: "Playfair Display", Georgia, serif;
    font-size: 25px;
    font-weight: 500;
}

.fw-description,
.fw-community-intro {
    color: #987888;
    font-size: 13px;
    line-height: 1.8;
    margin: 16px 0 24px;
}

/* FORM */

.fw-field {
    margin: 22px 0;
}

.fw-field label {
    display: block;
    color: #85415c;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.2px;
    margin-bottom: 10px;
}

.fw-field label span {
    color: #c72c50;
}

.fw-field select,
.fw-field textarea,
.fw-comment-form input,
.fw-reply-form input {
    box-sizing: border-box;
    width: 100%;
    border: 1px solid #ebcbd7;
    border-radius: 12px;
    background: #fff9fb;
    color: #654352;
    padding: 13px 14px;
    font: inherit;
    font-size: 13px;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.fw-field select:focus,
.fw-field textarea:focus,
.fw-comment-form input:focus,
.fw-reply-form input:focus {
    border-color: #c77b98;
    box-shadow: 0 0 0 3px rgba(199, 123, 152, 0.13);
}

.fw-field textarea {
    min-height: 150px;
    resize: vertical;
    line-height: 1.8;
}

.fw-field textarea::placeholder,
.fw-comment-form input::placeholder,
.fw-reply-form input::placeholder {
    color: #bd9eac;
}

.fw-character-note {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 9px;
    color: #ad8798;
    font-size: 10px;
}

.fw-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #80566a;
    font-size: 12px;
    cursor: pointer;
}

.fw-checkbox input {
    accent-color: #b53c60;
    width: 16px;
    height: 16px;
}

.fw-notice {
    display: flex;
    gap: 12px;
    padding: 14px;
    margin: 20px 0;
    background: #fff0f4;
    border: 1px solid #f2d5df;
    border-radius: 13px;
}

.fw-notice-symbol {
    color: #bd5276;
    font-size: 18px;
}

.fw-notice p {
    margin: 0;
    color: #936b7d;
    font-size: 11px;
    line-height: 1.8;
}

.fw-submit,
.fw-button {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 12px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #c72c50, #8b2345);
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.fw-submit {
    width: 100%;
    padding: 15px 18px;
    margin-top: 20px;
    font-size: 13px;
}

.fw-button {
    padding: 11px 17px;
    font-size: 12px;
}

.fw-submit:hover,
.fw-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(127, 32, 60, 0.2);
}

.fw-button-symbol {
    font-size: 17px;
}

.fw-bottom-note {
    text-align: center;
    color: #b08a9b;
    font-size: 10px;
    margin: 17px 0 0;
}

/* SIDE QUOTE */

.fw-side-note {
    position: relative;
    overflow: hidden;
    padding: 25px;
    margin-top: 20px;
    border: 1px solid #efd0db;
    border-radius: 20px;
    background: linear-gradient(135deg, #fbe3eb, #fff6f8);
}

.fw-side-quote {
    color: #c77b98;
    font-family: Georgia, serif;
    font-size: 55px;
    line-height: 0.8;
}

.fw-side-note p {
    color: #7f203c;
    font-family: "Playfair Display", Georgia, serif;
    font-size: 20px;
    line-height: 1.6;
}

.fw-side-caption {
    color: #a45a76;
    font-size: 9px;
    letter-spacing: 1.5px;
}

.fw-side-decoration {
    display: block;
    text-align: right;
    color: #c77b98;
    margin-top: 15px;
}

/* COMMUNITY HEADING */

.fw-community-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.fw-community-heading h2 {
    font-size: 32px;
}

.fw-community-heading h2 span {
    color: #c72c50;
}

.fw-wall-symbol {
    display: grid;
    place-items: center;
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    border-radius: 50%;
    background: #f9dfe7;
    color: #b53c60;
    font-size: 24px;
}

.fw-community-intro {
    margin-top: 10px;
}

/* POST CARDS */

.fw-post-list {
    display: grid;
    gap: 20px;
}

.fw-post {
    padding: 24px;
    min-width: 0;
    transition: transform 0.2s, box-shadow 0.2s;
}

.fw-post:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 35px rgba(127, 32, 60, 0.11);
}

.fw-post-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}

.fw-post-category {
    padding: 7px 12px;
    border-radius: 30px;
    background: #fff0f4;
    color: #a13f61;
    font-size: 10px;
    font-weight: 700;
}

.fw-post-top time {
    color: #ae8b9b;
    font-size: 10px;
}

.fw-author {
    display: flex;
    align-items: center;
    gap: 12px;
}

.fw-avatar {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f8d5e1, #fcebf1);
    color: #a6385c;
    font-size: 21px;
}

.fw-author strong {
    display: block;
    color: #7f203c;
    font-size: 13px;
}

.fw-author small {
    display: block;
    color: #ad8999;
    font-size: 10px;
    margin-top: 4px;
}

.fw-post-content {
    margin: 22px 0;
    color: #654352;
    font-size: 14px;
    line-height: 1.9;
    overflow-wrap: anywhere;
    white-space: normal;
}

/* LIKE AND COMMENT ACTIONS */

.fw-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    padding: 15px 0;
    border-top: 1px solid #f3dfe6;
    border-bottom: 1px solid #f3dfe6;
}

.fw-actions form {
    margin: 0;
}

.fw-action-btn,
.fw-action-static {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 13px;
    border: 1px solid #efd0db;
    border-radius: 25px;
    background: #fff9fb;
    color: #9c4d6a;
    font-size: 11px;
}

.fw-action-btn {
    cursor: pointer;
}

.fw-action-btn:hover,
.fw-action-btn.is-liked {
    background: #fbe3eb;
    border-color: #e4a6bb;
    color: #a3284e;
}

.fw-action-btn span {
    font-weight: 700;
}

/* COMMENTS */

.fw-comments {
    margin-top: 22px;
}

.fw-comments h3 {
    color: #7f203c;
    font-family: "Playfair Display", Georgia, serif;
    font-size: 19px;
    font-weight: 500;
    margin: 0 0 18px;
}

.fw-comments h3 span {
    color: #c72c50;
}

.fw-comment {
    padding: 13px 14px;
    margin: 12px 0;
    border-left: 2px solid #e7b2c4;
    border-radius: 0 12px 12px 0;
    background: #fff7fa;
    overflow-wrap: anywhere;
}

.fw-comment-meta {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 8px;
}

.fw-comment-meta strong {
    color: #8d3655;
    font-size: 11px;
}

.fw-comment-meta span {
    color: #b08a9b;
    font-size: 10px;
}

.fw-comment > p {
    color: #765364;
    font-size: 12px;
    line-height: 1.8;
    margin: 0 0 10px;
    overflow-wrap: anywhere;
}

.fw-comment-form,
.fw-reply-form {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 14px;
}

.fw-comment-form input,
.fw-reply-form input {
    min-width: 0;
    flex: 1;
    padding: 11px 12px;
    font-size: 12px;
}

.fw-small-btn {
    padding: 9px 12px;
    border: 1px solid #e7bdcc;
    border-radius: 10px;
    background: #fff;
    color: #963c5c;
    font-size: 11px;
    cursor: pointer;
}

.fw-small-btn:hover {
    background: #fbe3eb;
}

.fw-delete-form {
    margin: 8px 0;
}

.fw-delete-btn {
    padding: 5px 10px;
    border: none;
    border-radius: 8px;
    background: #fff0f2;
    color: #a42d4b;
    font-size: 10px;
    cursor: pointer;
}

.fw-delete-btn:hover {
    background: #f8d8e1;
}

/* EMPTY WALL */

.fw-empty {
    padding: 45px 25px;
    text-align: center;
}

.fw-empty-symbol {
    color: #c77b98;
    font-size: 30px;
}

.fw-empty h3 {
    color: #7f203c;
    font-family: "Playfair Display", Georgia, serif;
    font-size: 24px;
    font-weight: 500;
}

.fw-empty p {
    max-width: 380px;
    margin: 0 auto;
    color: #987888;
    font-size: 13px;
    line-height: 1.9;
}

.fw-empty-decoration {
    display: block;
    margin-top: 22px;
    color: #c77b98;
}

.fw-alert {
    display: flex;
    gap: 12px;
    align-items: center;
    padding: 16px 20px;
    margin-bottom: 24px;
    border-radius: 14px;
    font-size: 13px;
}

.fw-success {
    background: #f0faf2;
    border: 1px solid #cce8d1;
    color: #326a42;
}

.fw-error {
    background: #fff0f2;
    border: 1px solid #f0cbd4;
    color: #a42d4b;
}

.fw-alert p {
    margin: 5px 0 0;
}

.fw-alert-icon {
    font-size: 23px;
}

/* PAGE FOOTERS */

.fw-community-footer,
.fw-page-footer {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    color: #ad8194;
    text-align: center;
    font-size: 11px;
}

.fw-community-footer {
    padding: 25px 10px;
}

.fw-page-footer {
    margin-top: 45px;
}

.fw-page-footer p {
    margin: 0;
}

/* RESPONSIVE */

@media (max-width: 900px) {
    .fw-layout {
        grid-template-columns: minmax(0, 1fr);
    }

    .fw-compose-column {
        order: 1;
    }

    .fw-community {
        order: 2;
    }

    .fw-hero {
        padding: 25px 10px;
    }
}

@media (max-width: 520px) {
    .freedom-wall {
        padding: 25px 12px 40px;
    }

    .fw-card,
    .fw-post {
        padding: 18px;
        border-radius: 17px;
    }

    .fw-hero h1 {
        font-size: 35px;
    }

    .fw-divider {
        gap: 7px;
        font-size: 8px;
        letter-spacing: 1px;
    }

    .fw-community-heading h2 {
        font-size: 26px;
    }

    .fw-comment-form,
    .fw-reply-form {
        align-items: stretch;
    }

    .fw-comment-form {
        flex-wrap: wrap;
    }

    .fw-comment-form input {
        flex-basis: 100%;
    }

    .fw-comment-form .fw-button {
        width: 100%;
    }

    .fw-post-top {
        align-items: flex-start;
    }
}
