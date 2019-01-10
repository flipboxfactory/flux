module.exports = {
    title: 'Flux',
    description: 'Manipulate complex data structures',
    base: '/',
    theme: 'flipbox',
    themeConfig: {
        logo: '/icon.svg',
        docsRepo: 'flipboxfactory/flux',
        docsDir: 'docs',
        docsBranch: 'master',
        editLinks: true,
        search: true,
        searchMaxSuggestions: 10,
        codeLanguages: {
            twig: 'Twig',
            php: 'PHP',
            json: 'JSON',
            // any other languages you want to include in code toggles...
        },
        nav: [
            {text: 'Documentation', link: 'https://flux.flipboxfactory.com'},
            {text: 'Changelog', link: 'https://github.com/flipboxfactory/flux/blob/master/CHANGELOG.md'},
            {text: 'Repo', link: 'https://github.com/flipboxfactory/flux'}
        ],
        sidebar: {
            '/': [
                {
                    title: 'Getting Started',
                    collapsable: true,
                    children: [
                        ['/', 'Introduction'],
                        ['/requirements', 'Requirements'],
                        ['/installation', 'Installation / Upgrading'],
                        ['/support', 'Support'],
                    ]
                }
            ]
        }
    },
    markdown: {
        anchor: { level: [2, 3, 4] },
        toc: { includeLevel: [3] },
        config(md) {
            md.use(require('vuepress-theme-flipbox/markup'))
        }
    }
}