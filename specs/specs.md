# ABTest

## Définition

Un test AB, AZ, ou multivarié est une technique de tests permettant dans un contexte unique de présenter et mesurer plusieurs variations afin d'isoler la variation la plus performante.

## Instanciation

La façon la plus simple d'instancier un test est d'instancier la classe Test dans le namespace AB, en lui attribuant un nom :

```
$myTest = new AB\Test('Simple test');
```

Le test est dorénavant prêt. Il ne reste plus qu'à l'executer à l'endroit où l'on veut l'utiliser gràce à un simple echo :

```
echo $myTest;
```

Dans cet exemple, il ne se passe évidemment rien. Il faut fournir quelques variations, ou tout du moins une variation par defaut sous forme de [closure](http://fr2.php.net/manual/fr/functions.anonymous.php) :

```
$myTest = new AB\Test('Simple test');

$myTest->addDefaultVariation(function(){

    echo sprintf('<h1>%s</h1>', 'Default title');

});

echo $myTest;
```

La variation par defaut peut aussi être fournie à l'objet Ab\Test lors de son instanciation :

```
$myTest = new AB\Test('Simple test', function(){

    echo sprintf('<h1>%s</h1>', 'Default title');

});

echo $myTest;
```

Le test précédent affichera donc en toutes circonstances le code suivant :

`<h1>Default title</h1>`

## Variations

Pour ajouter des variations à un test, il suffit d'utiliser la méthode idoine :

```
$myTest->addVariation('variation number one', function(){

    echo sprintf('<h1>%s</h1>', 'Fancy variation');

});
```

Cette méthode retourne un objet AB\Variation sur lequel on peut attribuer des poids afin d'affiner les chances de voir telle ou telle variation être testée :

```
$variation = $myTest->addVariation('variation number one', function(){

    echo sprintf('<h1>%s</h1>', 'Fancy variation');

});

$variation->withWeight(8);
```

Il est tout à fait possible de chainer ces appels pour une meilleure lisibilité du code :

```
$myTest->addVariation('variation number one', function(){

    echo sprintf('<h1>%s</h1>', 'Fancy variation');

})->withWeight(8);
```

Il est possible d'ajouter autant de variation que souhaitées. Elles seront selectionnées aléatoirement en fonction du poids qui leur aura été attribué (ou pas) :

```
$myTest->addVariation('variation number one', function(){

    echo sprintf('<h1>%s</h1>', 'Fancy variation');

})->withWeight(8);

$myTest->addVariation('second variation', function(){

    echo sprintf('<h1>%s</h1>', 'Nice variation');

});

$myTest->addVariation('variation #3', function(){

    echo sprintf('<h1>%s</h1>', 'Bright variation');

})->withWeight(5);
```

## Assertions

Dans certains cas, il est préférable de conditionner l'execution d'un test AB à un contexte particulier. C'est ce que permet la méthode `assert` : Si l'assertion est négative (retourne false) la variation par défaut sera executée

