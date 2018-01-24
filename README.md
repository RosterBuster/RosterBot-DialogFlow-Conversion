# Dialogflow Agent Merger

This command helps you merge two dialogflow agents together. If you have multiple agents you are working one and you are actively training on one of them, it can be hard to get the trained agent into the development agent. With this command you can keep your training that you did on the production version, but also keep the new/modified intents that you were working on.

## Usage

```
dfmerger merge <path-to-production-zip> <path-to-development-zip>
```

This will create a new zip in your directory called `dialogflow-merged`
