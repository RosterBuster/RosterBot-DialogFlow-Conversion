# Dialogflow Agent Merger

This command helps you merge two dialogflow agents together. If you have multiple agents you are working on, and you are actively training on one of them, it can be hard to get the trained agent into the development agent. With this command you can keep the training from the production version, but also keep the new/modified intents from the development agent.

## Installation

You can install this tool via composer by using the following command:

```
composer global require rosterbuster/dialogflow-conversion
```

## Usage

```
dfmerger merge <path-to-production-zip> <path-to-development-zip>
```

This will create a new zip in your directory called `dialogflow-merged`
