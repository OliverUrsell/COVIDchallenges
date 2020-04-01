
currentCharge = 5.66
currentCalls = 299

costPerCall = currentCharge/currentCalls

monthlyAdRevenuePerUser = (4266/12)/50000

monthlyUses = 16920
monthlyUses = 11000

if costPerCall*monthlyUses < 200:
    monthlyCost = 0
else:
    monthlyCost = costPerCall*monthlyUses - 200

monthlyProfit = monthlyAdRevenuePerUser*monthlyUses

print(monthlyProfit)
print(monthlyCost)
print(monthlyProfit - monthlyCost)
