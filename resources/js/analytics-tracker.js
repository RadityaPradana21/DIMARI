/**
 * DIMARI Analytics Tracker
 * Tracks user interactions and learning time
 */

class AnalyticsTracker {
    constructor() {
        this.startTime = Date.now();
        this.sessionDuration = 0;
        this.pageName = this.getCurrentPage();
        this.userId = null;
        this.isTracking = false;
        
        this.init();
    }
    
    init() {
        // Get user ID from session (this would be passed from PHP)
        this.userId = window.DIMARI_USER_ID || null;
        
        if (!this.userId) return;
        
        this.startTracking();
        this.setupEventListeners();
        
        // Track page visit
        this.trackPageVisit();
        
        // Track session end
        window.addEventListener('beforeunload', () => {
            this.endSession();
        });
        
        // Track visibility change (tab switching)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseTracking();
            } else {
                this.resumeTracking();
            }
        });
    }
    
    getCurrentPage() {
        const path = window.location.pathname;
        if (path.includes('dashboard.php')) return 'dashboard';
        if (path.includes('materi.php')) return 'materi';
        if (path.includes('quiz.php')) return 'quiz';
        if (path.includes('forum.php')) return 'forum';
        if (path.includes('achievements.php')) return 'achievements';
        if (path.includes('profile.php')) return 'profile';
        return 'unknown';
    }
    
    startTracking() {
        this.isTracking = true;
        this.startTime = Date.now();
        
        // Update session duration every minute
        this.trackingInterval = setInterval(() => {
            if (this.isTracking) {
                this.sessionDuration = Math.floor((Date.now() - this.startTime) / 1000 / 60); // in minutes
                this.updateLearningTime();
            }
        }, 60000); // Every minute
    }
    
    pauseTracking() {
        this.isTracking = false;
    }
    
    resumeTracking() {
        if (!this.isTracking) {
            this.startTime = Date.now() - (this.sessionDuration * 60 * 1000);
            this.isTracking = true;
        }
    }
    
    endSession() {
        if (this.sessionDuration > 0) {
            this.updateLearningTime();
        }
        
        if (this.trackingInterval) {
            clearInterval(this.trackingInterval);
        }
    }
    
    trackPageVisit() {
        if (!this.userId) return;
        
        fetch('<?= BASE_URL ?>/api/track_page_visit.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                page: this.pageName,
                timestamp: Date.now()
            })
        }).catch(err => console.log('Analytics tracking failed:', err));
    }
    
    updateLearningTime() {
        if (!this.userId || this.sessionDuration <= 0) return;
        
        fetch('<?= BASE_URL ?>/api/update_learning_time.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                minutes: this.sessionDuration,
                page: this.pageName
            })
        }).catch(err => console.log('Learning time update failed:', err));
    }
    
    setupEventListeners() {
        // Track module interactions
        const moduleLinks = document.querySelectorAll('a[href*="materi.php?id="]');
        moduleLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const moduleId = link.href.match(/id=(\d+)/)?.[1];
                if (moduleId) {
                    this.trackModuleInteraction(moduleId, 'view');
                }
            });
        });
        
        // Track quiz interactions
        const quizLinks = document.querySelectorAll('a[href*="quiz.php?module="]');
        quizLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const moduleId = link.href.match(/module=(\d+)/)?.[1];
                if (moduleId) {
                    this.trackQuizInteraction(moduleId, 'start');
                }
            });
        });
        
        // Track forum interactions
        const forumLinks = document.querySelectorAll('a[href*="forum.php"]');
        forumLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                this.trackForumInteraction('view');
            });
        });
    }
    
    trackModuleInteraction(moduleId, action) {
        if (!this.userId) return;
        
        fetch('<?= BASE_URL ?>/api/track_interaction.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                type: 'module',
                action: action,
                target_id: moduleId,
                timestamp: Date.now()
            })
        }).catch(err => console.log('Module interaction tracking failed:', err));
    }
    
    trackQuizInteraction(moduleId, action) {
        if (!this.userId) return;
        
        fetch('<?= BASE_URL ?>/api/track_interaction.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                type: 'quiz',
                action: action,
                target_id: moduleId,
                timestamp: Date.now()
            })
        }).catch(err => console.log('Quiz interaction tracking failed:', err));
    }
    
    trackForumInteraction(action) {
        if (!this.userId) return;
        
        fetch('<?= BASE_URL ?>/api/track_interaction.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                type: 'forum',
                action: action,
                timestamp: Date.now()
            })
        }).catch(err => console.log('Forum interaction tracking failed:', err));
    }
}

// Initialize tracker when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.DIMARI_ANALYTICS = new AnalyticsTracker();
});

// Export for manual tracking
window.DIMARI_TRACK = {
    trackEvent: (type, action, targetId = null) => {
        if (window.DIMARI_ANALYTICS) {
            if (type === 'module') {
                window.DIMARI_ANALYTICS.trackModuleInteraction(targetId, action);
            } else if (type === 'quiz') {
                window.DIMARI_ANALYTICS.trackQuizInteraction(targetId, action);
            } else if (type === 'forum') {
                window.DIMARI_ANALYTICS.trackForumInteraction(action);
            }
        }
    }
};
