# import asyncio
# import logging
# import uvicorn
#
# from src.bot.bot import bot, dp
# from src.webapp.app import app
#
#
# async def start_bot():
#     await dp.start_polling(bot)
#
# async def main():
#     bot_task = asyncio.create_task(start_bot())
#     uvicorn_task = asyncio.create_task(run_uvicorn())
#     await asyncio.gather(bot_task, uvicorn_task)
#
# async def run_uvicorn():
#     config = uvicorn.Config(app, host="0.0.0.0", port=8000)
#     server = uvicorn.Server(config)
#     await server.serve()
#
#
# logging.basicConfig(level=logging.INFO)
#
# if __name__ == "__main__":
#     logging.info("Запуск бота...")
#     asyncio.run(main())


import asyncio
import logging

from bot import dp
from bot import bot


# from src.webapp.app import app


async def start_bot():
    await dp.start_polling(bot)

async def main():
    bot_task = asyncio.create_task(start_bot())
    # uvicorn_task = asyncio.create_task(run_uvicorn())
    await bot_task
    # await asyncio.gather(bot_task, uvicorn_task)

# async def run_uvicorn():
#     config = uvicorn.Config(app, host="0.0.0.0", port=8000)
#     server = uvicorn.Server(config)
#     await server.serve()


logging.basicConfig(level=logging.INFO)

if __name__ == "__main__":
    logging.info("Запуск бота...")
    asyncio.run(main())
