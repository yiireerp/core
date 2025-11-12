# Publishing Checklist for Yiire Auth

## Pre-Publishing Setup

- [ ] Update `composer.json` with correct team email
- [ ] Update `README.md` with correct support email if needed
- [ ] Review all documentation for accuracy
- [ ] Test Docker build locally
- [ ] Test application with Docker Compose
- [ ] Verify all API endpoints work

## GitHub Repository Setup

### 1. Initialize Repository
```bash
cd /Volumes/Works/yiire/auth/yiire
git init
git add .
git commit -m "Initial release v1.0.0"
git branch -M main
```

### 2. Create GitHub Repository
- [ ] Go to https://github.com/new
- [ ] Repository name: `auth`
- [ ] Organization/User: `yiire`
- [ ] Description: "Multi-tenant authorization microservice with JWT authentication, RBAC, and comprehensive user management built on Laravel"
- [ ] Public repository
- [ ] Do NOT initialize with README (we already have one)

### 3. Push to GitHub
```bash
git remote add origin https://github.com/yiire-erp/auth.git
git push -u origin main
```

### 4. Create Release
```bash
git tag -a v1.0.0 -m "Initial release v1.0.0"
git push origin v1.0.0
```

- [ ] Go to Releases on GitHub
- [ ] Create new release from tag v1.0.0
- [ ] Copy release notes from CHANGELOG.md
- [ ] Publish release

### 5. Repository Settings
- [ ] Add repository description
- [ ] Add website URL: https://github.com/yiire-erp/auth
- [ ] Add topics: `laravel`, `authentication`, `jwt`, `multi-tenant`, `rbac`, `authorization`, `microservice`, `api`, `sanctum`, `php`
- [ ] Enable Issues
- [ ] Enable Discussions (optional)
- [ ] Enable Wiki (optional)

### 6. Branch Protection (Optional)
- [ ] Settings → Branches → Add rule for `main`
- [ ] Require pull request reviews before merging
- [ ] Require status checks to pass (GitHub Actions)
- [ ] Require branches to be up to date

## Packagist (Composer) Publishing

### 1. Submit to Packagist
- [ ] Go to https://packagist.org/packages/submit
- [ ] Log in with GitHub account
- [ ] Submit repository URL: `https://github.com/yiire-erp/auth`
- [ ] Click "Check" to validate

### 2. Configure Auto-Update
- [ ] Go to package page on Packagist
- [ ] Click "Update" next to GitHub icon
- [ ] Authorize GitHub webhook
- [ ] This enables auto-updates on new releases

### 3. Verify Package
- [ ] Package appears at https://packagist.org/packages/yiire/auth
- [ ] Stats are tracking
- [ ] Latest version is v1.0.0

## Docker Publishing

### Option 1: GitHub Container Registry (Recommended)

#### Setup
```bash
# Login to GHCR
echo $GITHUB_TOKEN | docker login ghcr.io -u USERNAME --password-stdin
```

#### Build and Push
```bash
# Build image
docker build -t ghcr.io/yiire-erp/auth:1.0.0 .

# Tag as latest
docker tag ghcr.io/yiire-erp/auth:1.0.0 ghcr.io/yiire-erp/auth:latest

# Push both tags
docker push ghcr.io/yiire-erp/auth:1.0.0
docker push ghcr.io/yiire-erp/auth:latest
```

#### Configure GitHub Package
- [ ] Go to package settings
- [ ] Make package public
- [ ] Link to repository
- [ ] Add description

### Option 2: Docker Hub

#### Setup
```bash
# Login to Docker Hub
docker login
```

#### Build and Push
```bash
# Build image
docker build -t yiire/auth:1.0.0 .

# Tag as latest
docker tag yiire/auth:1.0.0 yiire/auth:latest

# Push both tags
docker push yiire/auth:1.0.0
docker push yiire/auth:latest
```

#### Configure Docker Hub
- [ ] Add full description from README
- [ ] Add short description
- [ ] Set up automated builds (optional)

## GitHub Actions Configuration

### Secrets Required
- [ ] Add `CODECOV_TOKEN` for code coverage (optional)
- [ ] GitHub Actions already has `GITHUB_TOKEN` by default

### Verify Workflows
- [ ] `.github/workflows/tests.yml` runs on push
- [ ] `.github/workflows/docker.yml` builds images
- [ ] All tests pass
- [ ] Docker image builds successfully

## Documentation

### Update README Badges
Add to top of README.md after initial publish:

```markdown
[![Tests](https://github.com/yiire-erp/auth/workflows/Tests/badge.svg)](https://github.com/yiire-erp/auth/actions)
[![Latest Version](https://img.shields.io/github/v/release/yiire/auth)](https://github.com/yiire-erp/auth/releases)
[![Docker Pulls](https://img.shields.io/docker/pulls/yiire/auth)](https://hub.docker.com/r/yiire/auth)
[![Packagist Downloads](https://img.shields.io/packagist/dt/yiire/auth)](https://packagist.org/packages/yiire/auth)
```

## Community Setup

### Create Discussion Templates
- [ ] Create "Announcements" category
- [ ] Create "Q&A" category
- [ ] Create "Show and Tell" category
- [ ] Pin welcome message

### Security Policy
Create `.github/SECURITY.md`:
- [ ] Supported versions
- [ ] Reporting vulnerabilities
- [ ] Security update process

### Code of Conduct
Create `CODE_OF_CONDUCT.md`:
- [ ] Use standard Contributor Covenant
- [ ] Or create custom code of conduct

## Marketing and Promotion

### Social Media
- [ ] Tweet about release
- [ ] Post on Reddit (r/PHP, r/laravel)
- [ ] Post on dev.to
- [ ] Post on Laravel News

### Laravel Community
- [ ] Submit to Laravel News
- [ ] Share in Laravel Discord
- [ ] Share in Laracasts forum

### Developer Communities
- [ ] Post on Hacker News (Show HN)
- [ ] Post on Product Hunt
- [ ] Share on LinkedIn

## Monitoring and Maintenance

### Analytics Setup
- [ ] Enable GitHub Insights
- [ ] Monitor Packagist stats
- [ ] Monitor Docker Hub stats
- [ ] Track GitHub stars/forks

### Issue Management
- [ ] Respond to issues within 48 hours
- [ ] Triage and label issues
- [ ] Close stale issues
- [ ] Maintain project board

### Updates Schedule
- [ ] Security updates: Immediate
- [ ] Dependency updates: Monthly
- [ ] Feature releases: Quarterly
- [ ] Major versions: Yearly

## Post-Launch Checklist

### Week 1
- [ ] Monitor for critical issues
- [ ] Respond to early adopters
- [ ] Fix any deployment issues
- [ ] Update documentation based on feedback

### Month 1
- [ ] Gather user feedback
- [ ] Plan next release
- [ ] Update roadmap
- [ ] Write blog post about learnings

## Success Metrics

Track these metrics:
- [ ] GitHub stars: Target 100+ in first month
- [ ] Packagist downloads: Target 1000+ in first month
- [ ] Docker pulls: Target 500+ in first month
- [ ] Issues opened/closed ratio
- [ ] Pull requests merged
- [ ] Contributors count

## Notes

- Version format: MAJOR.MINOR.PATCH (SemVer)
- Release schedule: As needed for security, quarterly for features
- Support channels: GitHub Issues, Discussions, Email
- Response time goal: 48 hours for issues

---

## Quick Commands Reference

```bash
# Tag new release
git tag -a vX.Y.Z -m "Release vX.Y.Z"
git push origin vX.Y.Z

# Build Docker image
docker build -t ghcr.io/yiire-erp/auth:X.Y.Z .
docker push ghcr.io/yiire-erp/auth:X.Y.Z

# Run tests locally
php artisan test

# Code style check
./vendor/bin/pint --test

# Deploy with Docker
docker-compose up -d
```

---

**Last Updated:** November 12, 2025  
**Current Version:** 1.0.0  
**Status:** Ready for Publishing
