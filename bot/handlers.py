

from aiogram import Router
from aiogram.filters import CommandStart
from aiogram.types import Message

from keyboards import settings
from cache.accessor import get_redis_connection, on_shutdown


router = Router()


@router.message(CommandStart())
async def send_welcome(message: Message):
    try:
        await message.answer(
            "Привет! Нажми на кнопку, "
            "чтобы перейти узнать сколько осталось до твоего дня рождения.",
            reply_markup=settings)
        users_data = {
            "id": message.from_user.id,
            "first_name": message.from_user.first_name,
            "last_name": message.from_user.last_name if message.from_user.last_name else "Фамилия не указана",
            "username": message.from_user.username,
            "birthday": message.from_user.birthdate,
        }

        # Открытие Redis
        redis_client = await get_redis_connection()

        # Запись значений в Redis
        for key, value in users_data.items():
            await redis_client.set(f"{key}", value)

        # Закрытие Redis
        await on_shutdown(redis_client)
    except Exception as e:
        print(e)

