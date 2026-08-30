import axios from 'axios';
import './echo';
import './course-chat';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
