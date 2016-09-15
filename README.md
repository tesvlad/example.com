  - midRateCreator(MidRateHistoryFactoryInterface)
    
    - registry of mids creators (MidRateHistoryCreatorRegistryInterface[])
      - concrette mid creator (MidRateHistoryCreatorInterface)
          - collection of strategies (MidStrategyCollectionInterface)
            - concrette strategy (MidStrategyInterface)
            - concrette strategy2 (MidStrategyInterface)
      
      - concrette mid creator2
        - all the same

        
Q: How to add new mid

A: Add migration, add developer fixture, create service mid_rate_history.creator.mid.some_mid and inject own MidStrategyCollectionInterface into MidRateHistoryCreator
Example
    mid_rate_history.creator.mid.qiwi:
        class: MidRate\Application\Service\MidRateHistory\Factory\CreateFromMid\MidRateHistoryCreator
        arguments:
            - '@mid_rate_history.mid_stategy.qiwi_strategy_collecion'
            
Q: How to create stategy
A: Create some Class with MidStrategyInterface implementation

Q: How to create mid stategy collection (MidStrategyCollectionInterface)

A: Create some service like and inject any MidStrategyInterface into collection
    mid_rate_history.mid_stategy.qiwi_strategy_collecion:
        class: MidRate\Application\Service\MidRateHistory\Factory\CreateFromMid\MidStrategy\MidStrategyCollection\MidStrategyCollection
        arguments:
            -
                - '@mid_rate_history.mid_stategy.qiwi_cross_rates'

