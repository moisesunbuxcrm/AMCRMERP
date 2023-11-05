module.exports = {
    "env": {
        "browser": true,
        "es6": true
    },
    "extends": "eslint:recommended",
    "parserOptions": {
        "ecmaFeatures": {
            "experimentalObjectRestSpread": true,
            "jsx": true
        }
    },
    "plugins": [
        "babel",
        "react"
    ],
    "rules": {
        "indent": [
            "error",
            "tab"
        ],
        "linebreak-style": [
            "error",
            "unix"
        ],
        "quotes": [
            "error",
            "double"
        ],
        "semi": [
            "error",
            "always"
        ],
        "react/jsx-uses-react": "error",
        "react/jsx-uses-vars": "error",
        "import/no-extraneous-dependencies": ["error", {
          "devDependencies": ['*.config.js']
        }],
        "no-multi-spaces": [1, {
          "exceptions": { "VariableDeclaration": true, "ImportDeclaration": true }
        }]
        }
};