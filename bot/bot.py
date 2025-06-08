from aiogram import Bot, Dispatcher

# from src.bot.config import TOKEN
from handlers import router

bot = Bot(token="7895010754:AAFkUxVfH77ScYoSqQiEe0CHX7Forb5BPjs")

dp = Dispatcher()

dp.include_router(router)
